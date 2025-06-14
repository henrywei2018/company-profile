{{-- resources/views/client/quotations/show.blade.php --}}
<x-layouts.client :title="'Quotation: ' . $quotation->project_type">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('client.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path>
                    </svg>
                    <a href="{{ route('client.quotations.index') }}" class="ml-1 md:ml-2 text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        My Quotations
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path>
                    </svg>
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ $quotation->project_type }}
                    </span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Status Alert -->
    @if($quotation->isExpired())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md dark:bg-red-900/20 dark:border-red-800 dark:text-red-200">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"></path>
                </svg>
                This quotation has expired. Please submit a new request if you're still interested.
            </div>
        </div>
    @elseif($quotation->status === 'approved' && !$quotation->project_created)
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md dark:bg-green-900/20 dark:border-green-800 dark:text-green-200">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
                </svg>
                Great news! Your quotation has been approved. Our team will contact you soon to begin the project.
            </div>
        </div>
    @elseif($quotation->status === 'rejected')
        <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-md dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-200">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"></path>
                </svg>
                This quotation request was not approved. Please see the feedback below or submit a new request.
            </div>
        </div>
    @endif

    <!-- Quotation Header -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-6 py-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-4">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $quotation->project_type }}</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $quotation->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $quotation->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                            {{ $quotation->status === 'reviewed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                            {{ $quotation->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                            {{ $quotation->status === 'expired' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}">
                            {{ ucfirst($quotation->status) }}
                        </span>

                        @if($quotation->project_created)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Converted to Project
                            </span>
                        @endif
                    </div>

                    <!-- Meta Information -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"></path>
                            </svg>
                            <div>
                                <span class="block font-medium">Submitted</span>
                                {{ $quotation->created_at->format('M d, Y \a\t g:i A') }}
                            </div>
                        </div>

                        @if($quotation->service)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                                <div>
                                    <span class="block font-medium">Service</span>
                                    {{ $quotation->service->title }}
                                </div>
                            </div>
                        @endif

                        @if($quotation->budget)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"></path>
                                </svg>
                                <div>
                                    <span class="block font-medium">Budget</span>
                                    {{ $quotation->budget }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 lg:mt-0 lg:ml-6 flex flex-col sm:flex-row gap-3">
                    @if($quotation->project_created)
                        <a href="{{ route('client.projects.show', $quotation->project) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            View Project
                        </a>
                    @endif

                    @if(in_array($quotation->status, ['pending', 'reviewed']) && !$quotation->isExpired())
                        <button onclick="openEditModal()" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Request
                        </button>
                    @endif

                    <a href="{{ route('client.quotations.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Quotations
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Project Requirements -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Requirements</h3>
                </div>
                <div class="p-6">
                    @if($quotation->requirements)
                        <div class="prose prose-sm max-w-none dark:prose-invert">
                            {!! nl2br(e($quotation->requirements)) !!}
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic">No specific requirements provided.</p>
                    @endif
                </div>
            </div>

            <!-- Timeline & Budget -->
            @if($quotation->start_date || $quotation->deadline || $quotation->timeline)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Timeline & Budget</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($quotation->start_date)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Preferred Start Date</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $quotation->start_date->format('M d, Y') }}</p>
                            </div>
                        @endif

                        @if($quotation->deadline)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Deadline</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $quotation->deadline->format('M d, Y') }}</p>
                            </div>
                        @endif

                        @if($quotation->timeline)
                            <div class="md:col-span-2">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Timeline Notes</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $quotation->timeline }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Contact Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Contact Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Name</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $quotation->name }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Email</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $quotation->email }}</p>
                        </div>

                        @if($quotation->phone)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Phone</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $quotation->phone }}</p>
                            </div>
                        @endif

                        @if($quotation->company)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Company</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $quotation->company }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            @if($quotation->attachments && $quotation->attachments->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Attachments</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($quotation->attachments as $attachment)
                            <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $attachment->original_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attachment->human_readable_size }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('client.quotations.attachments.download', [$quotation, $attachment]) }}" 
                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"></path>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Timeline -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Status Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <!-- Submitted -->
                            <li>
                                <div class="relative pb-8">
                                    <div class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600"></div>
                                    <div class="relative flex space-x-3">
                                        <div class="bg-green-500 h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Quotation Submitted</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->created_at->format('M d, Y \a\t g:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- Reviewed -->
                            <li>
                                <div class="relative pb-8">
                                    @if(!in_array($quotation->status, ['pending']))
                                        <div class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600"></div>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div class="{{ in_array($quotation->status, ['reviewed', 'approved', 'rejected']) ? 'bg-blue-500' : 'bg-gray-300' }} h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm font-medium {{ in_array($quotation->status, ['reviewed', 'approved', 'rejected']) ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">
                                                Under Review
                                            </p>
                                            @if(in_array($quotation->status, ['reviewed', 'approved', 'rejected']))
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Reviewed by our team</p>
                                            @else
                                                <p class="text-xs text-gray-400">Pending review</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- Decision -->
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div class="{{ $quotation->status === 'approved' ? 'bg-green-500' : ($quotation->status === 'rejected' ? 'bg-red-500' : 'bg-gray-300') }} h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                            @if($quotation->status === 'approved')
                                                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                                                </svg>
                                            @elseif($quotation->status === 'rejected')
                                                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"></path>
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm font-medium {{ $quotation->status === 'approved' || $quotation->status === 'rejected' ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">
                                                @if($quotation->status === 'approved')
                                                    Approved
                                                @elseif($quotation->status === 'rejected')
                                                    Not Approved
                                                @else
                                                    Awaiting Decision
                                                @endif
                                            </p>
                                            @if($quotation->status === 'approved' || $quotation->status === 'rejected')
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->updated_at->format('M d, Y') }}</p>
                                            @else
                                                <p class="text-xs text-gray-400">Pending decision</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>

                            @if($quotation->status === 'approved')
                            <!-- Project Creation -->
                            <li class="pt-8">
                                <div class="relative">
                                    <div class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600"></div>
                                    <div class="relative flex space-x-3">
                                        <div class="{{ $quotation->project_created ? 'bg-purple-500' : 'bg-gray-300' }} h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm font-medium {{ $quotation->project_created ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">
                                                Project Created
                                            </p>
                                            @if($quotation->project_created)
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Project initiated</p>
                                            @else
                                                <p class="text-xs text-gray-400">Will be created soon</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Admin Response -->
            @if($quotation->admin_notes)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Response from Our Team</h3>
                </div>
                <div class="p-6">
                    <div class="prose prose-sm max-w-none dark:prose-invert">
                        {!! nl2br(e($quotation->admin_notes)) !!}
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($quotation->status === 'rejected' || $quotation->isExpired())
                        <a href="{{ route('quotation.index') }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Submit New Request
                        </a>
                    @endif

                    <a href="{{ route('contact.index') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Contact Support
                    </a>

                    <button onclick="window.print()" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    @if(in_array($quotation->status, ['pending', 'reviewed']) && !$quotation->isExpired())
    <div id="editModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full mx-auto">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Quotation Request</h3>
                    <button onclick="closeEditModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('client.quotations.update', $quotation) }}" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label for="project_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Type</label>
                            <input type="text" name="project_type" id="project_type" 
                                   value="{{ $quotation->project_type }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        
                        <div>
                            <label for="requirements" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Requirements</label>
                            <textarea name="requirements" id="requirements" rows="4" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ $quotation->requirements }}</textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="budget" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Budget</label>
                                <input type="text" name="budget" id="budget" 
                                       value="{{ $quotation->budget }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            
                            <div>
                                <label for="timeline" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Timeline</label>
                                <input type="text" name="timeline" id="timeline" 
                                       value="{{ $quotation->timeline }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        function openEditModal() {
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });
    </script>
    @endpush

    @push('styles')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
    @endpush
</x-layouts.client>