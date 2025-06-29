{{-- resources/views/admin/testimonials/show.blade.php --}}
<x-layouts.admin title="View Testimonial">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Testimonials' => route('admin.testimonials.index'),
        $testimonial->client_name => ''
    ]" />

    <!-- Header -->
    <x-admin.header-section 
        :title="'Testimonial from ' . $testimonial->client_name" 
        description="View testimonial details and manage status">
        
        <x-slot name="additionalActions">
            <!-- Edit Button -->
            <a href="{{ route('admin.testimonials.edit', $testimonial) }}"
               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Testimonial
            </a>
        </x-slot>
    </x-admin.header-section>

    <div class="space-y-6">
        <!-- Status Overview -->
        <x-admin.card>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @if($testimonial->image)
                        <img src="{{ $testimonial->image_url }}" 
                             alt="{{ $testimonial->client_name }}"
                             class="h-16 w-16 rounded-full object-cover">
                    @else
                        <div class="h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                            <svg class="h-8 w-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                            </svg>
                        </div>
                    @endif
                    
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $testimonial->client_name }}</h2>
                        @if($testimonial->client_position || $testimonial->client_company)
                            <p class="text-gray-600 dark:text-gray-400">
                                @if($testimonial->client_position){{ $testimonial->client_position }}@endif
                                @if($testimonial->client_position && $testimonial->client_company) at @endif
                                @if($testimonial->client_company){{ $testimonial->client_company }}@endif
                            </p>
                        @endif
                        <div class="flex items-center mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-5 w-5 {{ $i <= $testimonial->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">({{ $testimonial->rating }}/5)</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2">
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
            </div>
        </x-admin.card>

        <!-- Testimonial Content -->
        <x-admin.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Testimonial Content</h3>
            </x-slot>

            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                <blockquote class="text-gray-900 dark:text-white text-lg leading-relaxed italic">
                    "{{ $testimonial->content }}"
                </blockquote>
            </div>
        </x-admin.card>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Client Details -->
            <x-admin.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Client Details</h3>
                </x-slot>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $testimonial->client_name }}</dd>
                    </div>

                    @if($testimonial->client_position)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Position</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $testimonial->client_position }}</dd>
                        </div>
                    @endif

                    @if($testimonial->client_company)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Company</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $testimonial->client_company }}</dd>
                        </div>
                    @endif

                    @if($testimonial->client)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Linked User Account</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="#" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                    {{ $testimonial->client->name }}
                                </a>
                            </dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rating</dt>
                        <dd class="mt-1 flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-4 w-4 {{ $i <= $testimonial->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ $testimonial->rating }}/5</span>
                        </dd>
                    </div>
                </dl>
            </x-admin.card>

            <!-- System Information -->
            <x-admin.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">System Information</h3>
                </x-slot>

                <dl class="space-y-4">
                    @if($testimonial->project)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Related Project</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="#" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                    {{ $testimonial->project->title }}
                                </a>
                            </dd>
                        </div>
                    @endif

                    @if(isset($testimonial->status))
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white capitalize">{{ $testimonial->status }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Active</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $testimonial->is_active ? 'Yes' : 'No' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Featured</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $testimonial->featured ? 'Yes' : 'No' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $testimonial->created_at->format('M j, Y \a\t g:i A') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $testimonial->updated_at->format('M j, Y \a\t g:i A') }}</dd>
                    </div>

                    @if($testimonial->approved_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $testimonial->approved_at->format('M j, Y \a\t g:i A') }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</dt>
                        <dd class="mt-1 text-sm text-gray-500 dark:text-gray-400">#{{ $testimonial->id }}</dd>
                    </div>
                </dl>
            </x-admin.card>
        </div>

        <!-- Admin Notes -->
        @if($testimonial->admin_notes)
            <x-admin.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Admin Notes</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Internal notes visible only to administrators</p>
                </x-slot>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <p class="text-sm text-gray-900 dark:text-white">{{ $testimonial->admin_notes }}</p>
                </div>
            </x-admin.card>
        @endif

        <!-- Quick Actions -->
        <x-admin.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
            </x-slot>

            <div class="flex flex-wrap gap-3">
                @if(isset($testimonial->status) && $testimonial->status === 'pending')
                    <form action="{{ route('admin.testimonials.approve', $testimonial) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                            </svg>
                            Approve
                        </button>
                    </form>
                @endif

                <form action="{{ route('admin.testimonials.toggle-featured', $testimonial) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md {{ $testimonial->featured ? 'text-purple-700 bg-purple-50 border-purple-300' : 'text-gray-700 bg-white' }} hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        {{ $testimonial->featured ? 'Remove Featured' : 'Set Featured' }}
                    </button>
                </form>

                <form action="{{ route('admin.testimonials.toggle-active', $testimonial) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md {{ $testimonial->is_active ? 'text-red-700 bg-red-50 border-red-300' : 'text-green-700 bg-green-50 border-green-300' }} hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ $testimonial->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>

                <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" 
                      class="inline" onsubmit="return confirm('Are you sure you want to delete this testimonial?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </x-admin.card>
    </div>
</x-layouts.admin>