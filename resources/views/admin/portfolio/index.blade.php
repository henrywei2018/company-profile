<!-- resources/views/admin/portfolio/index.blade.php -->
<x-admin-layout :title="'Portfolio Management'">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Manage Portfolio</h2>
        <div class="mt-4 md:mt-0 flex flex-col md:flex-row gap-3">
            <form action="{{ route('admin.portfolio.index') }}" method="GET" class="flex items-center space-x-2">
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search portfolio items..."
                        value="{{ request()->get('search') }}"
                        class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button type="submit" class="text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <select
                    name="category"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    onchange="this.form.submit()"
                >
                    <option value="">All Categories</option>
                    <option value="residential" {{ request()->get('category') === 'residential' ? 'selected' : '' }}>Residential</option>
                    <option value="commercial" {{ request()->get('category') === 'commercial' ? 'selected' : '' }}>Commercial</option>
                    <option value="industrial" {{ request()->get('category') === 'industrial' ? 'selected' : '' }}>Industrial</option>
                    <option value="infrastructure" {{ request()->get('category') === 'infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                </select>
                <select
                    name="status"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    onchange="this.form.submit()"
                >
                    <option value="">All Status</option>
                    <option value="completed" {{ request()->get('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="in_progress" {{ request()->get('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="planned" {{ request()->get('status') === 'planned' ? 'selected' : '' }}>Planned</option>
                </select>
            </form>
            <a href="{{ route('admin.portfolio.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Portfolio Item
            </a>
        </div>
    </div>

    <!-- Portfolio Items Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if(isset($portfolioItems) && $portfolioItems->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Project
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                        </th>
                        <th scope="col" class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($portfolioItems as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($item->getFeaturedImageUrlAttribute())
                                            <img src="{{ $item->getFeaturedImageUrlAttribute() }}" alt="{{ $item->title }}" class="h-10 w-10 rounded-md object-cover">
                                        @else
                                            <div class="h-10 w-10 rounded-md bg-gray-200 flex items-center justify-center text-gray-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $item->title }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $item->location ?? 'No location specified' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->category }}</div>
                                @if($item->client_name)
                                    <div class="text-sm text-gray-500">{{ $item->client_name }}</div>
                                @endif
                            </td>
                            <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $item->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($item->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                       'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                </span>
                                @if($item->featured)
                                    <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Featured
                                    </span>
                                @endif
                            </td>
                            <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($item->start_date && $item->end_date)
                                    {{ $item->start_date->format('M d, Y') }} - {{ $item->end_date->format('M d, Y') }}
                                @elseif($item->year)
                                    {{ $item->year }}
                                @else
                                    Not specified
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-3">
                                    <a href="{{ route('admin.portfolio.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('portfolio.show', $item->slug) }}" target="_blank" class="text-green-600 hover:text-green-900" title="View on site">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.portfolio.toggle-featured', $item->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="{{ $item->featured ? 'text-purple-600 hover:text-purple-900' : 'text-gray-400 hover:text-gray-600' }}" title="{{ $item->featured ? 'Remove from featured' : 'Mark as featured' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.portfolio.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this portfolio item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-200">
                {{ $portfolioItems->links('components.pagination') }}
            </div>
        @else
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No portfolio items found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ request()->has('search') || request()->has('category') || request()->has('status') 
                        ? 'Try adjusting your search or filter criteria.' 
                        : 'Get started by creating a new portfolio item.' }}
                </p>
                <div class="mt-6">
                    @if(request()->has('search') || request()->has('category') || request()->has('status'))
                        <a href="{{ route('admin.portfolio.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Clear Filters
                        </a>
                    @else
                        <a href="{{ route('admin.portfolio.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Portfolio Item
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Portfolio Statistics -->
    <div class="mt-8 bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Portfolio Overview</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Total Projects
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ $totalProjects ?? 0 }}
                        </dd>
                    </div>
                </div>

                <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Featured Projects
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-purple-600">
                            {{ $featuredProjects ?? 0 }}
                        </dd>
                    </div>
                </div>

                <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Completed Projects
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-green-600">
                            {{ $completedProjects ?? 0 }}
                        </dd>
                    </div>
                </div>

                <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            In Progress Projects
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-blue-600">
                            {{ $inProgressProjects ?? 0 }}
                        </dd>
                    </div>
                </div>
            </dl>
        </div>
    </div>

    <!-- Portfolio Categories -->
    <div class="mt-8 bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Portfolio Categories</h3>
            <button type="button" onclick="document.getElementById('createCategoryModal').classList.remove('hidden')" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Category
            </button>
        </div>
        <div class="p-6">
            @if(isset($categories) && $categories->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($categories as $category)
                        <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $category->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $category->portfolioItems_count ?? 0 }} items</p>
                            </div>
                            <div class="flex space-x-2">
                                <button type="button" onclick="editCategory('{{ $category->id }}', '{{ $category->name }}', '{{ $category->slug }}')" class="text-indigo-600 hover:text-indigo-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <form action="{{ route('admin.portfolio.categories.destroy', $category->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this category? All portfolio items will be uncategorized.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-gray-500">No portfolio categories found. Create a category to organize your portfolio items.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Category Modal -->
    <div id="createCategoryModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add New Category</h3>
                <button type="button" onclick="document.getElementById('createCategoryModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.portfolio.categories.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" name="name" id="name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="slug" class="block text-sm font-medium text-gray-700">Slug (optional)</label>
                    <input type="text" name="slug" id="slug" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-xs text-gray-500">Leave blank to generate automatically from name.</p>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="document.getElementById('createCategoryModal').classList.add('hidden')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Category</h3>
                <button type="button" onclick="document.getElementById('editCategoryModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editCategoryForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="edit_name" class="block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" name="name" id="edit_name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="edit_slug" class="block text-sm font-medium text-gray-700">Slug</label>
                    <input type="text" name="slug" id="edit_slug" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-xs text-gray-500">Leave blank to generate automatically from name.</p>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="document.getElementById('editCategoryModal').classList.add('hidden')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

@push('scripts')
<script>
    function editCategory(id, name, slug) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_slug').value = slug;
        document.getElementById('editCategoryForm').action = "{{ route('admin.portfolio.categories.update', '') }}/" + id;
        document.getElementById('editCategoryModal').classList.remove('hidden');
    }
</script>
@endpush