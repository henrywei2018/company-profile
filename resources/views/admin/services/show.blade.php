<!-- resources/views/admin/services/show.blade.php -->
<x-layouts.admin title="Service Details" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Services Management' => route('admin.services.index'),
            $service->title => '#'
        ]" />
        
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <x-admin.button
                href="{{ route('services.show', $service->slug) }}"
                color="light"
                type="button"
                target="_blank"
            >
                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View on Website
            </x-admin.button>
            
            <x-admin.button
                href="{{ route('admin.services.edit', $service) }}"
                color="primary"
                type="button"
            >
                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Service
            </x-admin.button>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Service Details -->
            <x-admin.card>
                <x-slot name="title">{{ $service->title }}</x-slot>
                
                @if($service->image)
                    <div class="mb-6">
                        <img src="{{ asset('storage/' . $service->image) }}" 
                             alt="{{ $service->title }}" 
                             class="w-full h-64 object-cover rounded-lg border">
                    </div>
                @endif
                
                @if($service->short_description)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Short Description</h4>
                        <p class="text-gray-600 dark:text-gray-400">{{ $service->short_description }}</p>
                    </div>
                @endif
                
                @if($service->description)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Full Description</h4>
                        <div class="prose prose-sm dark:prose-invert max-w-none">
                            {!! nl2br(e($service->description)) !!}
                        </div>
                    </div>
                @endif
                
                <!-- Service Statistics -->
                @if($service->quotations_count > 0 || $service->projects_count > 0)
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-4">Service Statistics</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ $service->quotations->count() }}
                                </div>
                                <div class="text-sm text-blue-600 dark:text-blue-400">Total Quotations</div>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $service->projects->count() ?? 0 }}
                                </div>
                                <div class="text-sm text-green-600 dark:text-green-400">Completed Projects</div>
                            </div>
                        </div>
                    </div>
                @endif
            </x-admin.card>
            
            <!-- Recent Quotations -->
            @if($service->quotations->count() > 0)
                <x-admin.card>
                    <x-slot name="title">Recent Quotations</x-slot>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Client
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($service->quotations->take(5) as $quotation)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $quotation->client_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $quotation->client_email }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $quotation->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : '' }}
                                                {{ $quotation->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : '' }}
                                                {{ $quotation->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : '' }}">
                                                {{ ucfirst($quotation->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $quotation->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.quotations.show', $quotation) }}" 
                                               class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($service->quotations->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.quotations.index', ['service_id' => $service->id]) }}" 
                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                View all {{ $service->quotations->count() }} quotations →
                            </a>
                        </div>
                    @endif
                </x-admin.card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Service Info -->
            <x-admin.card>
                <x-slot name="title">Service Information</x-slot>
                
                <div class="space-y-4">
                    @if($service->icon)
                        <div class="flex items-center justify-center">
                            <img src="{{ asset('storage/' . $service->icon) }}" 
                                 alt="{{ $service->title }} Icon" 
                                 class="w-16 h-16 object-cover rounded">
                        </div>
                    @endif
                    
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Category:</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $service->category->name ?? 'Uncategorized' }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Slug:</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                                {{ $service->slug }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Status:</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $service->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                {{ $service->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Featured:</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $service->featured ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $service->featured ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Sort Order:</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $service->sort_order ?? 0 }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Created:</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $service->created_at->format('M d, Y') }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Updated:</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $service->updated_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- SEO Information -->
            @if($service->seo)
                <x-admin.card>
                    <x-slot name="title">SEO Information</x-slot>
                    
                    <div class="space-y-3">
                        @if($service->seo->title)
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Meta Title:</span>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $service->seo->title }}</p>
                            </div>
                        @endif
                        
                        @if($service->seo->description)
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Meta Description:</span>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $service->seo->description }}</p>
                            </div>
                        @endif
                        
                        @if($service->seo->keywords)
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Keywords:</span>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $service->seo->keywords }}</p>
                            </div>
                        @endif
                    </div>
                </x-admin.card>
            @endif

            <!-- Quick Actions -->
            <x-admin.card>
                <x-slot name="title">Quick Actions</x-slot>
                
                <div class="space-y-3">
                    <!-- Toggle Active Status -->
                    <form action="{{ route('admin.services.toggle-active', $service) }}" method="POST" class="w-full">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                            </svg>
                            {{ $service->is_active ? 'Deactivate' : 'Activate' }} Service
                        </button>
                    </form>
                    
                    <!-- Toggle Featured Status -->
                    <form action="{{ route('admin.services.toggle-featured', $service) }}" method="POST" class="w-full">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            {{ $service->featured ? 'Remove from Featured' : 'Mark as Featured' }}
                        </button>
                    </form>
                    
                    <!-- View Quotations -->
                    <a href="{{ route('admin.quotations.index', ['service_id' => $service->id]) }}" 
                       class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        View All Quotations
                    </a>
                </div>
            </x-admin.card>
        </div>
    </div>
</x-layouts.admin>