{{-- resources/views/client/quotations/show.blade.php --}}
<x-layouts.client title="Quotation Details - {{ $quotation->project_type }}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Dashboard' => route('client.dashboard'),
            'Quotations' => route('client.quotations.index'),
            $quotation->project_type => '#'
        ]" />
        
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <!-- Status Badge -->
            @php
                $statusConfig = [
                    'pending' => ['type' => 'warning', 'label' => 'Pending Review'],
                    'reviewed' => ['type' => 'info', 'label' => 'Under Review'], 
                    'approved' => ['type' => 'success', 'label' => 'Approved'],
                    'rejected' => ['type' => 'danger', 'label' => 'Rejected']
                ];
                $config = $statusConfig[$quotation->status] ?? ['type' => 'secondary', 'label' => ucfirst($quotation->status)];
            @endphp
            <x-admin.badge :type="$config['type']" size="lg">
                {{ $config['label'] }}
            </x-admin.badge>
            
            <!-- Expiry Status -->
            @if($expiryStatus['status'] === 'expiring_soon')
                <x-admin.badge type="warning" size="lg" class="animate-pulse">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Expires in {{ $expiryStatus['days_remaining'] }} days
                </x-admin.badge>
            @elseif($expiryStatus['status'] === 'expired')
                <x-admin.badge type="danger" size="lg">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Expired
                </x-admin.badge>
            @endif
        </div>
    </div>

    <!-- Alerts Section -->
    @if(!empty($quotationAlerts))
        <div class="mb-6 space-y-4">
            @foreach($quotationAlerts as $alert)
                <x-admin.alert :type="$alert['type']" :dismissible="false">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="font-medium">{{ $alert['title'] }}</h4>
                            <p class="mt-1">{{ $alert['message'] }}</p>
                        </div>
                        @if(isset($alert['action']))
                            <div class="ml-4 flex-shrink-0">
                                <x-admin.button 
                                    href="{{ $alert['action']['url'] }}" 
                                    :color="$alert['type'] === 'info' ? 'primary' : ($alert['type'] === 'warning' ? 'warning' : 'success')"
                                    size="sm"
                                >
                                    {{ $alert['action']['text'] }}
                                </x-admin.button>
                            </div>
                        @endif
                    </div>
                </x-admin.alert>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Quotation Overview -->
            <x-admin.card>
                <x-slot name="title">Quotation Overview</x-slot>
                <x-slot name="headerActions">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        #{{ $quotation->quotation_number ?? $quotation->id }}
                    </span>
                </x-slot>
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Project Type</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $quotation->project_type }}
                            </p>
                        </div>
                        
                        @if($quotation->service)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $quotation->service->title }}
                                </p>
                            </div>
                        @endif
                        
                        @if($quotation->location)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $quotation->location }}
                                </p>
                            </div>
                        @endif
                        
                        @if($quotation->budget_range)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Budget Range</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $quotation->budget_range }}
                                </p>
                            </div>
                        @endif
                        
                        @if($quotation->start_date)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preferred Start Date</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $quotation->start_date->format('M d, Y') }}
                                </p>
                            </div>
                        @endif
                        
                        @if($quotation->priority && $quotation->priority !== 'normal')
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</h3>
                                <x-admin.badge 
                                    :type="$quotation->priority === 'urgent' ? 'danger' : ($quotation->priority === 'high' ? 'warning' : 'info')"
                                >
                                    {{ ucfirst($quotation->priority) }}
                                </x-admin.badge>
                            </div>
                        @endif
                    </div>
                </div>
            </x-admin.card>

            <!-- Requirements -->
            <x-admin.card title="Project Requirements">
                <div class="p-6">
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($quotation->requirements)) !!}
                    </div>
                    
                    @if($quotation->additional_info)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional Information</h4>
                            <div class="prose dark:prose-invert max-w-none">
                                {!! nl2br(e($quotation->additional_info)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Attachments -->
            @if($quotation->attachments->count() > 0)
                <x-admin.card title="Attachments">
                    <div class="p-6">
                        <x-admin.list>
                            @foreach($quotation->attachments as $attachment)
                                <x-admin.list-item>
                                    <div class="flex items-center justify-between py-3">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $attachment->filename }}
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $this->formatFileSize($attachment->size) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div>
                                            <x-admin.button 
                                                href="{{ route('client.quotations.download-attachment', [$quotation, $attachment]) }}"
                                                color="light" 
                                                size="sm"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Download
                                            </x-admin.button>
                                        </div>
                                    </div>
                                </x-admin.list-item>
                            @endforeach
                        </x-admin.list>
                    </div>
                </x-admin.card>
            @endif

            <!-- Project Information (if converted) -->
            @if($quotation->hasProject())
                <x-admin.card>
                    <x-slot name="title">Project Information</x-slot>
                    <x-slot name="headerActions">
                        <x-admin.badge type="success">Converted to Project</x-admin.badge>
                    </x-slot>
                    
                    <div class="p-6">
                        @php $project = $quotation->getExistingProject(); @endphp
                        @if($project)
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $project->title }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Created on {{ $quotation->project_created_at?->format('M d, Y') }}
                                    </p>
                                </div>
                                <x-admin.button 
                                    href="{{ route('client.projects.show', $project) }}" 
                                    color="primary"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                    View Project
                                </x-admin.button>
                            </div>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">
                                This quotation has been marked as converted to a project, but the project details are not available.
                            </p>
                        @endif
                    </div>
                </x-admin.card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            
            <!-- Quick Actions -->
            @if($quotation->status === 'approved' && !$quotation->client_approved && !$quotation->isExpired())
                <x-admin.card title="Quick Actions">
                    <div class="p-6 space-y-3">
                        <x-admin.button 
                            href="{{ route('client.quotations.approve', $quotation) }}" 
                            color="success" 
                            size="full"
                            onclick="return confirm('Are you sure you want to approve this quotation?')"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Approve Quotation
                        </x-admin.button>
                        
                        <x-admin.button 
                            href="{{ route('client.quotations.decline', $quotation) }}" 
                            color="danger" 
                            size="full"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Decline
                        </x-admin.button>
                    </div>
                </x-admin.card>
            @endif

            <!-- Timeline -->
            <x-admin.card title="Timeline">
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                    <div class="relative flex items-start space-x-3">
                                        <div class="relative">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-900 dark:text-white">Quotation Submitted</span>
                                                </div>
                                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $quotation->created_at->format('M d, Y \a\t g:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            @if($quotation->reviewed_at)
                                <li>
                                    <div class="relative pb-8">
                                        @if($quotation->approved_at || $quotation->status === 'rejected')
                                            <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-900 dark:text-white">Under Review</span>
                                                    </div>
                                                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $quotation->reviewed_at->format('M d, Y \a\t g:i A') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            
                            @if($quotation->approved_at)
                                <li>
                                    <div class="relative pb-8">
                                        @if($quotation->client_approved_at)
                                            <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-900 dark:text-white">Approved by Admin</span>
                                                    </div>
                                                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $quotation->approved_at->format('M d, Y \a\t g:i A') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            
                            @if($quotation->client_approved_at)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-green-600 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-900 dark:text-white">Client Approved</span>
                                                    </div>
                                                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $quotation->client_approved_at->format('M d, Y \a\t g:i A') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            
                            @if($quotation->status === 'rejected')
                                <li>
                                    <div class="relative">
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-900 dark:text-white">Rejected</span>
                                                    </div>
                                                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                                        Status updated
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </x-admin.card>

            <!-- Contact Information -->
            <x-admin.card title="Need Help?">
                <div class="p-6 space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Have questions about your quotation? We're here to help.
                    </p>
                    
                    <x-admin.button 
                        href="{{ route('client.messages.create', ['subject' => 'Question about Quotation #' . ($quotation->quotation_number ?? $quotation->id)]) }}" 
                        color="primary" 
                        size="full"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Send Message
                    </x-admin.button>
                </div>
            </x-admin.card>
        </div>
    </div>

    <!-- Related Quotations -->
    @if($relatedQuotations->count() > 0)
        <div class="mt-8">
            <x-admin.card title="Related Quotations">
                <div class="p-6">
                    <x-admin.list>
                        @foreach($relatedQuotations as $related)
                            <x-admin.list-item>
                                <div class="flex items-center justify-between py-3">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800/30 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h2v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $related->project_type }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $related->service ? $related->service->title : 'General Inquiry' }} • 
                                                {{ $related->created_at->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        @php
                                            $relatedStatusConfig = [
                                                'pending' => ['type' => 'warning', 'label' => 'Pending'],
                                                'reviewed' => ['type' => 'info', 'label' => 'Reviewed'], 
                                                'approved' => ['type' => 'success', 'label' => 'Approved'],
                                                'rejected' => ['type' => 'danger', 'label' => 'Rejected']
                                            ];
                                            $relatedConfig = $relatedStatusConfig[$related->status] ?? ['type' => 'secondary', 'label' => ucfirst($related->status)];
                                        @endphp
                                        <x-admin.badge :type="$relatedConfig['type']" size="sm">
                                            {{ $relatedConfig['label'] }}
                                        </x-admin.badge>
                                        
                                        <x-admin.button 
                                            href="{{ route('client.quotations.show', $related) }}" 
                                            color="light" 
                                            size="sm"
                                        >
                                            View
                                        </x-admin.button>
                                    </div>
                                </div>
                            </x-admin.list-item>
                        @endforeach
                    </x-admin.list>
                </div>
            </x-admin.card>
        </div>
    @endif
</x-layouts.client>

@push('scripts')
<script>
// Helper function to format file sizes
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Auto-refresh status if quotation is pending
@if($quotation->status === 'pending' || $quotation->status === 'reviewed')
    setInterval(function() {
        // Check for status updates every 30 seconds
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== '{{ $quotation->status }}') {
                location.reload();
            }
        })
        .catch(error => {
            // Silently handle errors
        });
    }, 30000);
@endif
</script>
@endpush