<x-layouts.admin title="Testimonials Management">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Testimonials' => '']" />

    <!-- Header Section -->
    <x-admin.header-section 
        title="Testimonials Management" 
        description="Manage client testimonials and reviews"
        :createRoute="route('admin.testimonials.create')"
        createText="Create New Testimonial">
        
        <x-slot name="additionalActions">
            <!-- Statistics Button -->
            <button type="button" 
                    onclick="showStatistics()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistics
            </button>
        </x-slot>
    </x-admin.header-section>

    <!-- Filter Section -->
    <x-admin.filter-section 
        :action="route('admin.testimonials.index')"
        :searchValue="request('search')"
        searchPlaceholder="Search by client name, company, or content..."
        :hasActiveFilters="request()->hasAny(['search', 'project_id', 'status', 'rating'])"
        :clearFiltersRoute="route('admin.testimonials.index')"
        :filters="[
            [
                'name' => 'project_id',
                'label' => 'Project',
                'allLabel' => 'All Projects',
                'options' => $projects->pluck('title', 'id')->toArray()
            ],
            [
                'name' => 'status', 
                'label' => 'Status',
                'allLabel' => 'All Status',
                'options' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive', 
                    'featured' => 'Featured',
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected'
                ]
            ],
            [
                'name' => 'rating',
                'label' => 'Min Rating',
                'allLabel' => 'All Ratings',
                'options' => [
                    '5' => '5 Stars',
                    '4' => '4+ Stars',
                    '3' => '3+ Stars'
                ]
            ]
        ]" />

    <!-- Bulk Actions -->
    <x-admin.bulk-actions 
        formId="bulk-form"
        :actionRoute="route('admin.testimonials.bulk-action')"
        selectedCountText="testimonials selected"
        :actions="[
            [
                'value' => 'activate',
                'label' => 'Activate',
                'bgColor' => 'bg-green-100',
                'textColor' => 'text-green-700',
                'hoverColor' => 'bg-green-200'
            ],
            [
                'value' => 'deactivate', 
                'label' => 'Deactivate',
                'bgColor' => 'bg-yellow-100',
                'textColor' => 'text-yellow-700',
                'hoverColor' => 'bg-yellow-200'
            ],
            [
                'value' => 'feature',
                'label' => 'Set as Featured', 
                'bgColor' => 'bg-purple-100',
                'textColor' => 'text-purple-700',
                'hoverColor' => 'bg-purple-200'
            ],
            [
                'value' => 'approve',
                'label' => 'Approve', 
                'bgColor' => 'bg-blue-100',
                'textColor' => 'text-blue-700',
                'hoverColor' => 'bg-blue-200'
            ],
            [
                'value' => 'delete',
                'label' => 'Delete', 
                'bgColor' => 'bg-red-100',
                'textColor' => 'text-red-700',
                'hoverColor' => 'bg-red-200'
            ]
        ]" />

    <!-- Data Table -->
    <x-admin.new.data-table 
        :items="$testimonials"
        emptyTitle="No testimonials found"
        emptyDescription="Get started by creating your first testimonial."
        emptyActionText="Create your first testimonial"
        :emptyActionRoute="route('admin.testimonials.create')"
        :hasActiveFilters="request()->hasAny(['search', 'project_id', 'status', 'rating'])"
        :clearFiltersRoute="route('admin.testimonials.index')"
        :headers="[
            ['label' => 'Client'],
            ['label' => 'Content & Rating'], 
            ['label' => 'Project'],
            ['label' => 'Status'],
            ['label' => 'Created']
        ]">

        <x-slot name="emptyIcon">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 0v10a2 2 0 002 2h6a2 2 0 002-2V8m-9 4h4"/>
        </x-slot>

        @foreach($testimonials as $testimonial)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="px-6 py-4">
                    <input type="checkbox" name="testimonial_ids[]" value="{{ $testimonial->id }}" 
                           class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </td>
                
                <!-- Client Info -->
                <td class="px-6 py-4">
                    <div class="flex items-start space-x-3">
                        <x-admin.media-preview 
                            :src="$testimonial->image ? asset('storage/' . $testimonial->image) : null"
                            :alt="$testimonial->client_name"
                            size="12"
                            iconClass="w-6 h-6"
                            type="user" />
                        
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $testimonial->client_name }}
                            </div>
                            @if($testimonial->client_position || $testimonial->client_company)
                                <div class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    @if($testimonial->client_position){{ $testimonial->client_position }}@endif
                                    @if($testimonial->client_position && $testimonial->client_company) at @endif
                                    @if($testimonial->client_company){{ $testimonial->client_company }}@endif
                                </div>
                            @endif
                            @if($testimonial->client)
                                <div class="text-xs text-blue-600 dark:text-blue-400">
                                    Linked to user account
                                </div>
                            @endif
                        </div>
                    </div>
                </td>

                <!-- Content & Rating -->
                <td class="px-6 py-4">
                    <div class="max-w-xs">
                        <p class="text-sm text-gray-900 dark:text-white line-clamp-2 mb-2">
                            {{ Str::limit($testimonial->content, 80) }}
                        </p>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-4 w-4 {{ $i <= $testimonial->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="ml-1 text-sm text-gray-500 dark:text-gray-400">({{ $testimonial->rating }})</span>
                        </div>
                    </div>
                </td>

                <!-- Project -->
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($testimonial->project)
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ Str::limit($testimonial->project->title, 30) }}
                        </div>
                    @else
                        <span class="text-sm text-gray-500 dark:text-gray-400">No project</span>
                    @endif
                </td>

                <!-- Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex flex-col space-y-1">
                        @if(isset($testimonial->status))
                            <x-admin.status-badge 
                                :status="$testimonial->status"
                                :colors="[
                                    'pending' => 'yellow',
                                    'approved' => 'green',
                                    'rejected' => 'red',
                                    'featured' => 'purple'
                                ]" />
                        @endif
                        
                        <x-admin.status-badge 
                            :status="$testimonial->is_active ? 'active' : 'inactive'"
                            :colors="[
                                'active' => 'green',
                                'inactive' => 'gray'
                            ]" />
                        
                        @if($testimonial->featured)
                            <x-admin.status-badge 
                                status="featured"
                                :colors="['featured' => 'purple']" />
                        @endif
                    </div>
                </td>

                <!-- Created Date -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $testimonial->created_at->format('M j, Y') }}
                    <div class="text-xs text-gray-400">
                        {{ $testimonial->created_at->format('g:i A') }}
                    </div>
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                        <!-- Quick Actions -->
                        @if(isset($testimonial->status) && $testimonial->status === 'pending')
                            <form method="POST" action="{{ route('admin.testimonials.approve', $testimonial) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                        title="Approve">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                    </svg>
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.testimonials.toggle-featured', $testimonial) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="{{ $testimonial->featured ? 'text-purple-600 hover:text-purple-900' : 'text-gray-400 hover:text-purple-600' }} transition"
                                    title="{{ $testimonial->featured ? 'Remove from Featured' : 'Set as Featured' }}">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.testimonials.toggle-active', $testimonial) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="{{ $testimonial->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }} transition"
                                    title="{{ $testimonial->is_active ? 'Deactivate' : 'Activate' }}">
                                @if($testimonial->is_active)
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                @endif
                            </button>
                        </form>

                        <!-- Dropdown Menu -->
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white dark:bg-gray-800 py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                
                                <a href="{{ route('admin.testimonials.show', $testimonial) }}" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View Details
                                </a>

                                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>

                                <div class="border-t border-gray-100 dark:border-gray-600"></div>

                                <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" 
                                      class="inline" onsubmit="return confirm('Are you sure you want to delete this testimonial?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.new.data-table>

    <script>
        function showStatistics() {
            fetch('{{ route("admin.testimonials.statistics") }}')
                .then(response => response.json())
                .then(data => {
                    alert(`Statistics:\nTotal: ${data.total}\nActive: ${data.active}\nFeatured: ${data.featured}\nPending: ${data.pending}\nAvg Rating: ${data.average_rating}/5`);
                });
        }
    </script>
</x-layouts.admin>