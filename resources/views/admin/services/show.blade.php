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
                        <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" class="w-full h-auto rounded-lg object-cover">
                    </div>
                @endif
                
                @if($service->short_description)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Summary</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $service->short_description }}</p>
                    </div>
                @endif
                
                <div class="prose max-w-none dark:prose-invert prose-img:rounded-lg prose-headings:text-gray-900 dark:prose-headings:text-white prose-p:text-gray-600 dark:prose-p:text-gray-400">
                    {!! $service->description !!}
                </div>
            </x-admin.card>
            
            <!-- Related Projects -->
            @if($service->quotations->count() > 0)
                <x-admin.card class="mt-6">
                    <x-slot name="title">Related Quotations</x-slot>
                    <x-slot name="subtitle">Quotations that requested this service</x-slot>
                    
                    <div class="divide-y divide-gray-200 dark:divide-neutral-700">
                        @foreach($service->quotations->take(5) as $quotation)
                            <div class="py-4 first:pt-0 last:pb-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">
                                            <a href="{{ route('admin.quotations.show', $quotation) }}" class="hover:underline">
                                                {{ $quotation->name }} - {{ $quotation->company }}
                                            </a>
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $quotation->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <x-admin.badge type="{{ $quotation->status === 'pending' ? 'warning' : ($quotation->status === 'approved' ? 'success' : 'danger') }}">
                                        {{ ucfirst($quotation->status) }}
                                    </x-admin.badge>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($service->quotations->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.quotations.index', ['service_id' => $service->id]) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                View all {{ $service->quotations->count() }} quotations
                            </a>
                        </div>
                    @endif
                </x-admin.card>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Service Status Card -->
            <x-admin.card>
                <x-slot name="title">Status Information</x-slot>
                
                <ul class="divide-y divide-gray-200 dark:divide-neutral-700">
                    <li class="py-3 first:pt-0 flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Status</span>
                        <span>
                            @if($service->is_active)
                                <x-admin.badge type="success" dot="true">Active</x-admin.badge>
                            @else
                                <x-admin.badge type="danger" dot="true">Inactive</x-admin.badge>
                            @endif
                        </span>
                    </li>
                    <li class="py-3 flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Featured</span>
                        <span>
                            @if($service->featured)
                                <x-admin.badge type="primary" dot="true">Featured</x-admin.badge>
                            @else
                                <x-admin.badge type="default">No</x-admin.badge>
                            @endif
                        </span>
                    </li>
                    <li class="py-3 flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Category</span>
                        <span class="text-gray-900 dark:text-white">
                            @if($service->category)
                                {{ $service->category->name }}
                            @else
                                <span class="text-gray-400 dark:text-gray-500">None</span>
                            @endif
                        </span>
                    </li>
                    <li class="py-3 flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Sort Order</span>
                        <span class="text-gray-900 dark:text-white">{{ $service->sort_order ?: 'Default' }}</span>
                    </li>
                    <li class="py-3 flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Created</span>
                        <span class="text-gray-900 dark:text-white">{{ $service->created_at->format('M d, Y') }}</span>
                    </li>
                    <li class="py-3 last:pb-0 flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Last Updated</span>
                        <span class="text-gray-900 dark:text-white">{{ $service->updated_at->format('M d, Y') }}</span>
                    </li>
                </ul>
            </x-admin.card>
            
            <!-- Meta Information -->
            <x-admin.card>
                <x-slot name="title">SEO Information</x-slot>
                
                <ul class="divide-y divide-gray-200 dark:divide-neutral-700">
                    <li class="py-3 first:pt-0">
                        <span class="block text-gray-600 dark:text-gray-400 mb-1">Meta Title</span>
                        <span class="block text-gray-900 dark:text-white">
                            {{ $service->seo->title ?? $service->title }}
                        </span>
                    </li>
                    <li class="py-3">
                        <span class="block text-gray-600 dark:text-gray-400 mb-1">Meta Description</span>
                        <span class="block text-gray-900 dark:text-white">
                            {{ $service->seo->description ?? ($service->short_description ?? 'Not set') }}
                        </span>
                    </li>
                    <li class="py-3 last:pb-0">
                        <span class="block text-gray-600 dark:text-gray-400 mb-1">Meta Keywords</span>
                        <span class="block text-gray-900 dark:text-white">
                            @if($service->seo && $service->seo->keywords)
                                @foreach(explode(',', $service->seo->keywords) as $keyword)
                                    <x-admin.badge class="mr-1 mb-1">{{ trim($keyword) }}</x-admin.badge>
                                @endforeach
                            @else
                                <span class="text-gray-400 dark:text-gray-500">Not set</span>
                            @endif
                        </span>
                    </li>
                </ul>
            </x-admin.card>
            
            <!-- Service Icon -->
            @if($service->icon)
                <x-admin.card>
                    <x-slot name="title">Service Icon</x-slot>
                    
                    <div class="flex justify-center p-4">
                        <img src="{{ asset('storage/' . $service->icon) }}" alt="{{ $service->title }} Icon" class="max-h-32">
                    </div>
                </x-admin.card>
            @endif
        </div>
    </div>
</x-layouts.admin>